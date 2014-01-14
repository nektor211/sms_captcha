function x2 = classify(x)
for i = 1:9
	x2(:,i) = (x==i);
end
x2 = x2';
